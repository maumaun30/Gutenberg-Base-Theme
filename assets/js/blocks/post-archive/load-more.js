/**
 * FUNaloMAX Post Archive — Load More (vanilla JS, no deps)
 */
(function () {
  'use strict';

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.fnlmx-post-archive__load-more');
    if (!btn) return;

    e.preventDefault();

    const gridId  = btn.dataset.grid;
    const blockId = btn.dataset.block;
    const nonce   = btn.dataset.nonce;
    const grid    = document.getElementById(gridId);
    const block   = document.querySelector(`[data-block-id="${blockId}"]`);

    if (!grid || !block) return;

    const postType = block.dataset.postType || 'post';
    const perPage  = parseInt(block.dataset.perPage, 10) || 4;
    let   page     = parseInt(block.dataset.currentPage, 10) || 1;
    page += 1;

    btn.disabled    = true;
    btn.textContent = 'Loading…';

    const body = new URLSearchParams({
      action   : fnlmxArchive.action,
      nonce    : nonce,
      post_type: postType,
      per_page : perPage,
      page     : page,
    });

    fetch(fnlmxArchive.ajaxUrl, {
      method : 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
      body   : body.toString(),
    })
      .then((r) => r.json())
      .then((res) => {
        if (!res.success) throw new Error('AJAX error');

        const { html, has_more } = res.data;

        if (html) {
          const tmp = document.createElement('div');
          tmp.innerHTML = html;
          // Animate each card in
          Array.from(tmp.children).forEach((card, i) => {
            card.style.opacity    = '0';
            card.style.transform  = 'translateY(20px)';
            card.style.transition = `opacity 0.4s ease ${i * 0.08}s, transform 0.4s ease ${i * 0.08}s`;
            grid.appendChild(card);
            // Trigger animation
            requestAnimationFrame(() => {
              requestAnimationFrame(() => {
                card.style.opacity   = '1';
                card.style.transform = 'translateY(0)';
              });
            });
          });
        }

        block.dataset.currentPage = page;

        if (!has_more) {
          btn.closest('.fnlmx-post-archive__load-more-wrap')?.remove();
        } else {
          btn.disabled    = false;
          btn.textContent = 'Load More';
        }
      })
      .catch(() => {
        btn.disabled    = false;
        btn.textContent = 'Load More';
      });
  });
})();