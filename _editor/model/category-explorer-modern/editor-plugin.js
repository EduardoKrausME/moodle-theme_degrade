editor => {
    const { Blocks } = editor;
    const category = { id: 'category-explorer-modern', label: 'lang::category_explorer_modern', icon: '<svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3m2 2v4h4V5H5m8-2h8v8h-8V3m2 2v4h4V5h-4M3 13h8v8H3v-8m2 2v4h4v-4H5m8-2h8v8h-8v-8m2 2v4h4v-4h-4Z"/></svg>' };
    Blocks.add('category-explorer-card', {
        label: 'lang::new_category_card',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<a href="#" class="category-explorer-modern__card"><span class="category-explorer-modern__icon">✦</span><div><strong>Nova categoria</strong><small>10 cursos disponíveis</small></div><b>→</b></a>`
    }, { at: 0 });
}
