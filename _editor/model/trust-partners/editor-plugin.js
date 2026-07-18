editor => {
    const { Blocks } = editor;
    const category = { id: 'trust-partners', label: 'lang::trust_partners', icon: '<svg viewBox="0 0 24 24"><path d="M16 17v-2h-3v-2h3v-2l3 3-3 3M12 3 1 9l4 2.18v6L12 21l3.18-1.65A6.96 6.96 0 0 1 15 18v-.3L12 19l-5-2.5v-4.23L12 15l5-2.73v.81c.33-.05.66-.08 1-.08.34 0 .67.03 1 .08V11.18L23 9 12 3Z"/></svg>' };
    Blocks.add('trust-partner-logo', {
        label: 'lang::new_partner',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<div class="trust-partners__logo"><span class="trust-partners__mark">P</span><strong>lang::trust_partners_parceiro_9e7032</strong></div>`
    }, { at: 0 });
}
