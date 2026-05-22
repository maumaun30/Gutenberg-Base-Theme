import { useEffect, useState } from '@wordpress/element';
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
  Button,
} from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import './style.css';

export default function Edit({ attributes, setAttributes }) {
  const { postsPerCategory, showViewAll, selectedCategories, categoryOrder } = attributes;

  const [categories, setCategories]       = useState([]);
  const [gamesByCategory, setGamesByCategory] = useState({});
  const [loading, setLoading]             = useState(true);
  const [activeFilter, setActiveFilter]   = useState('all');

  useEffect(() => {
    apiFetch({ path: '/wp/v2/game_category?per_page=100&_fields=id,name,slug' })
      .then((terms) => setCategories(terms))
      .catch(console.error);
  }, []);

  useEffect(() => {
    if (categories.length === 0) return;
    setLoading(true);
    const activeCats = selectedCategories.length > 0
      ? categories.filter((c) => selectedCategories.includes(c.id))
      : categories;

    Promise.all(
      activeCats.map((cat) =>
        apiFetch({
          path: `/wp/v2/game?game_category=${cat.id}&per_page=${postsPerCategory}&_fields=id,title,meta,_links&_embed=1`,
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

  // Build ordered list of active cats, respecting categoryOrder
  const baseCats = selectedCategories.length > 0
    ? categories.filter((c) => selectedCategories.includes(c.id))
    : categories;

  const orderedCats = categoryOrder.length > 0
    ? [...baseCats].sort((a, b) => {
        const ia = categoryOrder.indexOf(a.id);
        const ib = categoryOrder.indexOf(b.id);
        return (ia === -1 ? 9999 : ia) - (ib === -1 ? 9999 : ib);
      })
    : baseCats;

  function toggleCategory(id, checked) {
    if (checked) {
      setAttributes({ selectedCategories: [...selectedCategories, id] });
    } else {
      setAttributes({ selectedCategories: selectedCategories.filter((c) => c !== id) });
    }
  }

  function moveCat(id, direction) {
    const base = categoryOrder.length > 0 ? [...categoryOrder] : orderedCats.map((c) => c.id);
    const idx  = base.indexOf(id);
    if (idx === -1) return;
    const newIdx = idx + direction;
    if (newIdx < 0 || newIdx >= base.length) return;
    const updated = [...base];
    [updated[idx], updated[newIdx]] = [updated[newIdx], updated[idx]];
    setAttributes({ categoryOrder: updated });
  }

  const displayedCats = activeFilter === 'all'
    ? orderedCats
    : orderedCats.filter((c) => c.slug === activeFilter);

  return (
    <>
      <InspectorControls>
        <PanelBody title="Display Settings" initialOpen={true}>
          <RangeControl
            label="Games per category"
            value={postsPerCategory}
            onChange={(v) => setAttributes({ postsPerCategory: v })}
            min={1}
            max={12}
          />
          <ToggleControl
            label='Show "View All" button'
            checked={showViewAll}
            onChange={(v) => setAttributes({ showViewAll: v })}
          />
        </PanelBody>

        <PanelBody title="Filter Categories" initialOpen={false}>
          <p style={{ fontSize: 12, color: '#aaa', marginBottom: 8 }}>
            Leave all unchecked to show every category.
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
          <p style={{ fontSize: 12, color: '#aaa', marginBottom: 8 }}>
            Drag order using ↑ ↓ to set display sequence.
          </p>
          {orderedCats.map((cat, idx) => (
            <div key={cat.id} style={{ display: 'flex', alignItems: 'center', gap: 6, marginBottom: 6 }}>
              <span style={{ flex: 1, fontSize: 13, color: '#fff' }}>{cat.name}</span>
              <Button
                isSmall
                variant="secondary"
                disabled={idx === 0}
                onClick={() => moveCat(cat.id, -1)}
                aria-label="Move up"
              >↑</Button>
              <Button
                isSmall
                variant="secondary"
                disabled={idx === orderedCats.length - 1}
                onClick={() => moveCat(cat.id, 1)}
                aria-label="Move down"
              >↓</Button>
            </div>
          ))}
        </PanelBody>
      </InspectorControls>

      <section {...useBlockProps({ className: 'games-listing bg-dark-2 section' })}>
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

          {/* Game rows */}
          {loading ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <Spinner />
              <p style={{ color: 'rgba(255,255,255,0.4)', marginTop: 12, fontFamily: 'Montserrat,sans-serif', fontSize: '0.85rem' }}>
                Loading games…
              </p>
            </div>
          ) : displayedCats.length === 0 ? (
            <p style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center', fontFamily: 'Montserrat,sans-serif' }}>
              No categories found.
            </p>
          ) : (
            <div className="games-listing__categories">
              {displayedCats.map((cat, idx) => {
                const entry = gamesByCategory[cat.id];
                const games = entry?.games ?? [];

                return (
                  <div key={cat.id} className="games-listing__category" data-category={cat.slug}>

                    <div className="games-listing__cat-header">
                      <div className="games-listing__cat-header-left">
                        <h3 className="games-listing__cat-name">{cat.name}</h3>
                      </div>
                      {showViewAll && (
                        <div className="games-listing__cat-header-right">
                          <span className="games-listing__view-all-link">ALL</span>
                          <div className="games-listing__nav-btns">
                            <button className="games-listing__nav-btn">&#8249;</button>
                            <button className="games-listing__nav-btn">&#8250;</button>
                          </div>
                        </div>
                      )}
                    </div>

                    {/* Separator line */}
                    <div className="games-listing__cat-divider" />

                    <div className="games-listing__grid">
                      {games.length === 0 ? (
                        <p style={{ color: 'rgba(255,255,255,0.3)', fontFamily: 'Montserrat,sans-serif', fontSize: '0.8rem' }}>
                          No games in this category yet.
                        </p>
                      ) : (
                        games.map((game) => {
                          const thumb = game._embedded?.['wp:featuredmedia']?.[0]?.source_url ?? '';
                          const price = game.meta?.game_price ?? '';

                          return (
                            <div key={game.id} className="game-card">
                              <div className="game-card__image-wrap">
                                {thumb
                                  ? <img src={thumb} alt={game.title.rendered} className="game-card__image" />
                                  : <div className="game-card__image-placeholder">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
                                        <rect x="10" y="3" width="10" height="12" rx="2"/>
                                        <rect x="3" y="8" width="12" height="12" rx="2"/>
                                        <circle cx="6.5" cy="11.5" r="0.7" fill="currentColor"/>
                                        <circle cx="11.5" cy="11.5" r="0.7" fill="currentColor"/>
                                        <circle cx="6.5" cy="16.5" r="0.7" fill="currentColor"/>
                                        <circle cx="11.5" cy="16.5" r="0.7" fill="currentColor"/>
                                      </svg>
                                    </div>
                                }
                                <div className="game-card__overlay" aria-hidden="true" />
                                {price && <span className="game-card__price">{price}</span>}
                              </div>
                            </div>
                          );
                        })
                      )}
                    </div>

                  </div>
                );
              })}
            </div>
          )}

        </div>
      </section>
    </>
  );
}