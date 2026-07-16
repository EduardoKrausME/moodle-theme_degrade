editor => {
    const { Blocks } = editor;
    const category = { id: 'bento-features', label: 'lang::bento_features', icon: '<svg viewBox="0 0 24 24"><path d="M3 3h8v8H3V3m10 0h8v5h-8V3M3 13h8v8H3v-8m10-3h8v11h-8V10Z"/></svg>' };
    Blocks.add('bento-feature-card', {
        label: 'lang::new_feature_card',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<article class="bento-features__card"><div class="bento-features__icon">✦</div><span>Novo recurso</span><h3>Título do benefício</h3><p>Descreva de forma breve o valor deste recurso para os estudantes.</p></article>`
    }, { at: 0 });
}
