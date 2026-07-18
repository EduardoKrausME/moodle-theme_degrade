editor => {
    const { Blocks } = editor;
    const category = { id: 'stats-impact', label: 'lang::stats_impact', icon: '<svg viewBox="0 0 24 24"><path d="M3 3h2v18H3V3m16 8h2v10h-2V11m-8-6h2v16h-2V5m-4 8h2v8H7v-8m8-4h2v12h-2V9Z"/></svg>' };
    Blocks.add('stats-impact-item', {
        label: 'lang::new_statistic',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<div class="stats-impact__item"><div class="stats-impact__icon">+</div><strong>100+</strong><span>lang::new_statistic</span><small>lang::stats_impact_descricao_resultado_99495e</small></div>`
    }, { at: 0 });
}
