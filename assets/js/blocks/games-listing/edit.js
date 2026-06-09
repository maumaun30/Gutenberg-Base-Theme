import { useEffect, useState, useRef } from '@wordpress/element';
import {
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  RangeControl,
  ToggleControl,
  CheckboxControl,
  Spinner,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import './style.css';

/* ── Drag-and-drop sortable list ── */
function SortableList({ items, onReorder }) {
  const [draggingId, setDraggingId] = useState(null);
  const [overId, setOverId]         = useState(null);
  const dragNode = useRef(null);

  function handleDragStart(e, id) {
    setDraggingId(id);
    dragNode.current = e.currentTarget;
    e.dataTransfer.effectAllowed = 'move';
    setTimeout(() => { if (dragNode.current) dragNode.current.style.opacity = '0.4'; }, 0);
  }

  function handleDragEnter(id) {
    if (id !== draggingId) setOverId(id);
  }

  function handleDragEnd() {
    if (dragNode.current) dragNode.current.style.opacity = '1';
    dragNode.current = null;
    if (draggingId !== null && overId !== null && draggingId !== overId) {
      const from = items.findIndex((i) => i.id === draggingId);
      const to   = items.findIndex((i) => i.id === overId);
      const next = [...items];
      const [moved] = next.splice(from, 1);
      next.splice(to, 0, moved);
      onReorder(next.map((i) => i.id));
    }
    setDraggingId(null);
    setOverId(null);
  }

  function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
  }

  if (items.length === 0) {
    return (
      <p style={{ fontSize: 12, color: '#aaa', margin: '8px 0' }}>
        No categories to order yet. Check at least one category in "Filter Categories" first.
      </p>
    );
  }

  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
      {items.map((cat) => {
        const isDragging = cat.id === draggingId;
        const isOver     = cat.id === overId;
        return (
          <div
            key={cat.id}
            draggable
            onDragStart={(e) => handleDragStart(e, cat.id)}
            onDragEnter={() => handleDragEnter(cat.id)}
            onDragEnd={handleDragEnd}
            onDragOver={handleDragOver}
            style={{
              display: 'flex',
              alignItems: 'center',
              gap: 8,
              padding: '7px 10px',
              borderRadius: 6,
              background: isDragging ? 'rgba(247,29,194,0.08)' : isOver ? 'rgba(247,29,194,0.15)' : 'rgba(255,255,255,0.05)',
              border: isOver ? '1.5px dashed rgba(247,29,194,0.7)' : '1.5px solid rgba(255,255,255,0.08)',
              cursor: 'grab',
              userSelect: 'none',
              transition: 'background 0.15s, border-color 0.15s',
            }}
          >
            <span style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 2, opacity: 0.4, flexShrink: 0 }}>
              {[0,1,2,3,4,5].map((d) => (
                <span key={d} style={{ width: 3, height: 3, borderRadius: '50%', background: '#fff', display: 'block' }} />
              ))}
            </span>
            <span style={{ flex: 1, fontSize: 12, fontWeight: 600, color: '#000' }}>{cat.name}</span>
            <span style={{ fontSize: 10, color: 'rgba(255,255,255,0.25)' }}>⠿</span>
          </div>
        );
      })}
    </div>
  );
}

/* ── Main Edit component ── */
export default function Edit({ attributes, setAttributes }) {
  const {
    postsPerCategory,
    showViewAll,
    selectedCategories,
    categoryOrder,
    showRecommended,
    recommendedPerPage,
  } = attributes;

  const [categories, setCategories]           = useState([]);
  const [gamesByCategory, setGamesByCategory] = useState({});
  const [recGames, setRecGames]               = useState([]);
  const [loading, setLoading]                 = useState(true);
  const [activeFilter, setActiveFilter]       = useState('all');

  useEffect(() => {
    apiFetch({ path: '/wp/v2/game_category?per_page=100&_fields=id,name,slug' })
      .then((terms) => setCategories(terms))
      .catch(console.error);
  }, []);

  // Fetch Recommended games via game-tag slug
  useEffect(() => {
    if (!showRecommended) return;
    apiFetch({ path: '/wp/v2/game-tag?slug=recommended-games&_fields=id' })
      .then((tags) => {
        if (!tags.length) return;
        return apiFetch({
          path: `/wp/v2/game?game-tag=${tags[0].id}&per_page=${recommendedPerPage}&_fields=id,title,meta,_links&_embed=1`,
        });
      })
      .then((games) => games && setRecGames(games))
      .catch(console.error);
  }, [showRecommended, recommendedPerPage]);

  useEffect(() => {
    if (categories.length === 0) return;
    setLoading(true);
    const activeCats = selectedCategories.length > 0
      ? categories.filter((c) => selectedCategories.includes(c.id))
      : categories;

    Promise.all(
      activeCats.map((cat) =>
        apiFetch({
          path: `/wp/v2/game?game_category=${cat.id}&per_page=${postsPerCategory > 0 ? postsPerCategory : 100}&_fields=id,title,meta,_links&_embed=1`,
        }).then((games) => ({ cat, games }))
      )
    )
      .then((results) => {
        const map = {};
        results.forEach(({ cat, games }) => { map[cat.id] = { cat, games }; });
        setGamesByCategory(map);
        setLoading(false);
      })
      .catch((err) => { console.error(err); setLoading(false); });
  }, [categories, selectedCategories, postsPerCategory]);

  // Only show explicitly checked categories. Empty selection = show nothing.
  const baseCats = categories.filter((c) => selectedCategories.includes(c.id));

  const orderedCats = (() => {
    if (baseCats.length === 0) return [];
    if (categoryOrder.length === 0) return baseCats;
    const inOrder    = categoryOrder.map((id) => baseCats.find((c) => c.id === id)).filter(Boolean);
    const notInOrder = baseCats.filter((c) => !categoryOrder.includes(c.id));
    return [...inOrder, ...notInOrder];
  })();

  function toggleCategory(id, checked) {
    if (checked) {
      const newSelected = [...selectedCategories, id];
      const newOrder = categoryOrder.length > 0
        ? [...categoryOrder, id]
        : [...baseCats.map((c) => c.id), id];
      setAttributes({ selectedCategories: newSelected, categoryOrder: newOrder });
    } else {
      setAttributes({
        selectedCategories: selectedCategories.filter((c) => c !== id),
        categoryOrder: categoryOrder.filter((c) => c !== id),
      });
    }
  }

  const displayedCats = activeFilter === 'all'
    ? orderedCats
    : orderedCats.filter((c) => c.slug === activeFilter);

  /* ── Shared card renderer ── */
  function renderCard(game) {
    const thumb = game._embedded?.['wp:featuredmedia']?.[0]?.source_url ?? '';
    return (
      <div key={game.id} className="game-card">
        <div className="game-card__image-wrap">
          {thumb ? (
  <>
    {/* Blurred Background */}
    <img
      src={thumb}
      alt=""
      aria-hidden="true"
      className="game-card__image-bg"
    />

    {/* Main Image */}
    <img
      src={thumb}
      alt={game.title?.rendered ?? ''}
      className="game-card__image"
    />
  </>
) : (
  <div className="game-card__image-placeholder">
    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
      <rect x="10" y="3" width="10" height="12" rx="2"/>
      <rect x="3" y="8" width="12" height="12" rx="2"/>
      <circle cx="6.5" cy="11.5" r="0.7" fill="currentColor"/>
      <circle cx="11.5" cy="11.5" r="0.7" fill="currentColor"/>
      <circle cx="6.5" cy="16.5" r="0.7" fill="currentColor"/>
      <circle cx="11.5" cy="16.5" r="0.7" fill="currentColor"/>
    </svg>
  </div>
)}
          <div className="game-card__overlay" aria-hidden="true" />
        </div>
      </div>
    );
  }

  const NavBtns = () => (
    <div className="games-listing__nav-btns">
      <button className="games-listing__nav-btn" aria-label="Previous">
        <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg"><polygon points="8,0 8,12 0,6"/></svg>
      </button>
      <button className="games-listing__nav-btn" aria-label="Next">
        <svg viewBox="0 0 8 12" xmlns="http://www.w3.org/2000/svg"><polygon points="0,0 0,12 8,6"/></svg>
      </button>
    </div>
  );

  return (
    <>
      <InspectorControls>

        <PanelBody title="Display Settings" initialOpen={true}>
          <RangeControl
            label="Games per category"
            value={postsPerCategory}
            onChange={(v) => setAttributes({ postsPerCategory: v })}
            min={0} max={48}
            help="Set to 0 to show all games (no limit)."
          />
          <ToggleControl
            label='Show "View All" button'
            checked={showViewAll}
            onChange={(v) => setAttributes({ showViewAll: v })}
          />
        </PanelBody>

        <PanelBody title="Filter Categories" initialOpen={false}>
          <p style={{ fontSize: 12, color: '#aaa', marginBottom: 8 }}>
            Check the categories you want to display. Nothing will show until at least one is selected.
          </p>
          {categories.map((cat) => (
            <CheckboxControl
              key={cat.id}
              label={cat.name}
              checked={selectedCategories.includes(cat.id)}
              onChange={(checked) => toggleCategory(cat.id, checked)}
            />
          ))}
        </PanelBody>

        <PanelBody title="Category Order" initialOpen={false}>
          <p style={{ fontSize: 12, color: '#aaa', marginBottom: 10 }}>
            Drag the category boxes to reorder how they appear on the page.
          </p>
          <SortableList items={orderedCats} onReorder={(ids) => setAttributes({ categoryOrder: ids })} />
        </PanelBody>

        <PanelBody title="Recommended Games" initialOpen={false}>
          <p style={{ fontSize: 12, color: '#aaa', marginBottom: 8 }}>
            Games tagged <strong>Recommended Games</strong> via the Game Tags taxonomy are shown automatically in this section.
          </p>
          <ToggleControl
            label="Show Recommended Games section"
            checked={showRecommended}
            onChange={(v) => setAttributes({ showRecommended: v })}
          />
          {showRecommended && (
            <RangeControl
              label="Max recommended games"
              value={recommendedPerPage}
              onChange={(v) => setAttributes({ recommendedPerPage: v })}
              min={1} max={24}
            />
          )}
          {showRecommended && (
            <p style={{ fontSize: 11, color: '#aaa', marginTop: 8 }}>
              To add or remove games from this section, assign or remove the <em>Recommended Games</em> tag in the Game editor.
            </p>
          )}
        </PanelBody>

      </InspectorControls>

      <section {...useBlockProps({ className: 'games-listing bg-dark-3 section' })}>
        <div className="games-listing__glow" aria-hidden="true" />
        <div className="games-listing__container">

          {/* Category Tabs */}
          {!loading && categories.length > 0 && (
            <nav className="games-listing__tabs">
              <button
                className={`games-listing__tab is-hot${activeFilter === 'all' ? ' is-active' : ''}`}
                onClick={() => setActiveFilter('all')}
              >
                <svg className="games-listing__tab-icon" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M13.5 0.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
                </svg>
                HOT
              </button>
              {orderedCats.map((cat) => (
                <button
                  key={cat.id}
                  className={`games-listing__tab${activeFilter === cat.slug ? ' is-active' : ''}`}
                  onClick={() => setActiveFilter(cat.slug)}
                >
                  {cat.name}
                </button>
              ))}
            </nav>
          )}

          {loading ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <Spinner />
            </div>
          ) : baseCats.length === 0 ? (
            <div style={{
              padding: '2.5rem 1.5rem',
              border: '1px dashed rgba(247,29,194,0.3)',
              borderRadius: '0.625rem',
              textAlign: 'center',
              color: 'rgba(255,255,255,0.35)',
              fontFamily: 'Montserrat,sans-serif',
              fontSize: '0.82rem',
              lineHeight: 1.6,
            }}>
              No categories selected. Open <strong style={{ color: 'rgba(247,29,194,0.7)' }}>Filter Categories</strong> in the sidebar and check at least one category to display games.
            </div>
          ) : (
            <>
              {/* Category rows */}
              <div className="games-listing__categories">
                {displayedCats.map((cat) => {
                  const games = gamesByCategory[cat.id]?.games ?? [];
                  return (
                    <div key={cat.id} className="games-listing__category" data-category={cat.slug}>
                      <div className="games-listing__cat-header">
                        <div className="games-listing__cat-header-left">
                          <h3 className="games-listing__cat-name">{cat.name}</h3>
                        </div>
                        {showViewAll && (
                          <div className="games-listing__cat-header-right">
                            <span className="games-listing__view-all-link">ALL</span>
                            <NavBtns />
                          </div>
                        )}
                      </div>
                      <div className="games-listing__cat-divider" />
                      <div className="games-listing__grid">
                        {games.length === 0
                          ? <p style={{ color: 'rgba(255,255,255,0.3)', fontSize: '0.8rem' }}>No games yet.</p>
                          : games.map(renderCard)
                        }
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* Recommended Games row */}
              {showRecommended && recGames.length > 0 && (
                <div className="games-listing__category games-listing__recommended">
                  <div className="games-listing__cat-header">
                    <div className="games-listing__cat-header-left">
                      <h3 className="games-listing__cat-name">Recommended Games</h3>
                    </div>
                    <div className="games-listing__cat-header-right"><NavBtns /></div>
                  </div>
                  <div className="games-listing__cat-divider" />
                  <div className="games-listing__grid">{recGames.map(renderCard)}</div>
                </div>
              )}

              {/* Recommended empty hint */}
              {showRecommended && recGames.length === 0 && (
                <div style={{
                  marginTop: '2rem', padding: '1.5rem',
                  border: '1px dashed rgba(247,29,194,0.3)',
                  borderRadius: '0.625rem', textAlign: 'center',
                  color: 'rgba(255,255,255,0.3)', fontFamily: 'Montserrat,sans-serif', fontSize: '0.8rem',
                }}>
                  No games tagged <em>Recommended Games</em> yet. Assign the tag in the Game editor to populate this section.
                </div>
              )}
            </>
          )}

        </div>
      </section>
    </>
  );
}