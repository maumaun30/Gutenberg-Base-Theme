import { useEffect, useState } from '@wordpress/element';
import {
  RichText,
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

export default function Edit({ attributes, setAttributes }) {
  const {
    sectionLabel,
    heading,
    subheading,
    postsPerCategory,
    showViewAll,
    selectedCategories,
  } = attributes;

  const [categories, setCategories] = useState([]);
  const [gamesByCategory, setGamesByCategory] = useState({});
  const [loading, setLoading] = useState(true);

  // Fetch all game_category terms
  useEffect(() => {
    apiFetch({ path: '/wp/v2/game_category?per_page=100&_fields=id,name,slug' })
      .then((terms) => setCategories(terms))
      .catch(console.error);
  }, []);

  // Fetch games whenever selectedCategories or postsPerCategory changes
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
        results.forEach(({ cat, games }) => {
          map[cat.id] = { cat, games };
        });
        setGamesByCategory(map);
        setLoading(false);
      })
      .catch((err) => {
        console.error(err);
        setLoading(false);
      });
  }, [categories, selectedCategories, postsPerCategory]);

  const activeCats = selectedCategories.length > 0
    ? categories.filter((c) => selectedCategories.includes(c.id))
    : categories;

  function toggleCategory(id, checked) {
    if (checked) {
      setAttributes({ selectedCategories: [...selectedCategories, id] });
    } else {
      setAttributes({ selectedCategories: selectedCategories.filter((c) => c !== id) });
    }
  }

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
      </InspectorControls>

      <section {...useBlockProps({ className: 'games-listing bg-dark-2 section' })}>
        <div className="games-listing__glow" aria-hidden="true" />

        <div className="games-listing__container">

          {/* Header */}
          <div className="games-listing__header">
            <span className="games-listing__badge">
              <RichText
                tagName="span"
                value={sectionLabel}
                onChange={(v) => setAttributes({ sectionLabel: v })}
                placeholder="Game Library"
              />
            </span>
            <RichText
              tagName="h2"
              className="games-listing__heading"
              value={heading}
              onChange={(v) => setAttributes({ heading: v })}
              placeholder="Explore Our Games"
            />
            <RichText
              tagName="p"
              className="games-listing__subheading"
              value={subheading}
              onChange={(v) => setAttributes({ subheading: v })}
              placeholder="Subheading…"
            />
          </div>

          {/* Preview */}
          {loading ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <Spinner />
              <p style={{ color: 'rgba(255,255,255,0.5)', marginTop: 12 }}>
                Loading games…
              </p>
            </div>
          ) : activeCats.length === 0 ? (
            <p style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center' }}>
              No categories found. Create some under Games → Categories.
            </p>
          ) : (
            <div className="games-listing__categories">
              {activeCats.map((cat) => {
                const entry = gamesByCategory[cat.id];
                const games = entry?.games ?? [];

                return (
                  <div key={cat.id} className="games-listing__category">
                    <div className="games-listing__cat-header">
                      <div className="games-listing__cat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                          <rect x="2" y="7" width="20" height="14" rx="2" />
                          <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                          <line x1="12" y1="12" x2="12" y2="16" />
                          <line x1="10" y1="14" x2="14" y2="14" />
                        </svg>
                      </div>
                      <div>
                        <h3 className="games-listing__cat-name">{cat.name}</h3>
                        <div className="games-listing__cat-divider" />
                      </div>
                    </div>

                    <div className="games-listing__grid">
                      {games.length === 0 ? (
                        <p style={{ color: 'rgba(255,255,255,0.4)', gridColumn: '1/-1' }}>
                          No games in this category yet.
                        </p>
                      ) : (
                        games.map((game) => {
                          const thumb = game._embedded?.['wp:featuredmedia']?.[0]?.source_url ?? '';
                          const price = game.meta?.game_price ?? '';
                          const label = game.meta?.game_button_label || 'Play Now';

                          return (
                            <div key={game.id} className="game-card">
                              <div className="game-card__image-wrap">
                                {thumb
                                  ? <img src={thumb} alt={game.title.rendered} className="game-card__image" />
                                  : <div className="game-card__image-placeholder">No image</div>
                                }
                                <div className="game-card__overlay" aria-hidden="true" />
                                {price && <span className="game-card__price">{price}</span>}
                              </div>
                              <div className="game-card__body">
                                <h4
                                  className="game-card__title"
                                  dangerouslySetInnerHTML={{ __html: game.title.rendered }}
                                />
                                <div className="game-card__btn">{label}</div>
                              </div>
                              <div className="game-card__glow" aria-hidden="true" />
                            </div>
                          );
                        })
                      )}
                    </div>

                    {showViewAll && (
                      <div className="games-listing__view-all">
                        <div className="games-listing__view-btn">
                          View All {cat.name}
                        </div>
                      </div>
                    )}
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