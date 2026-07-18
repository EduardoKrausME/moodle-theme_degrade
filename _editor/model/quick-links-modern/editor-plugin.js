editor => {
    const { Blocks } = editor;
    const category = { id: 'quick-links-modern', label: 'lang::quick_links_modern', icon: '<svg viewBox="0 0 24 24"><path d="M10.59 13.41a2 2 0 0 1 0-2.82l2-2a2 2 0 0 1 2.82 2.82l-.79.79 1.42 1.42.79-.79a4 4 0 1 0-5.66-5.66l-2 2a4 4 0 0 0 0 5.66l.71.71 1.42-1.42-.71-.71m2.82-2.82-.71-.71-1.42 1.42.71.71a2 2 0 0 1 0 2.82l-2 2a2 2 0 0 1-2.82-2.82l.79-.79-1.42-1.42-.79.79a4 4 0 1 0 5.66 5.66l2-2a4 4 0 0 0 0-5.66Z"/></svg>' };
    Blocks.add('quick-link-item', {
        label: 'lang::new_quick_link',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<a class="quick-links-modern__item" href="#"><span class="quick-links-modern__icon">+</span><div><strong>lang::new_quick_link</strong><small>lang::quick_links_modern_descricao_acesso_c5d14b</small></div><b>→</b></a>`
    }, { at: 0 });
}
