editor => {
    const { Blocks } = editor;
    const category = { id: 'achievements-showcase', label: 'lang::achievements_showcase', icon: '<svg viewBox="0 0 24 24"><path d="M17 3H7v2H2v5c0 2.76 2.24 5 5 5 .84 0 1.64-.21 2.33-.58A5.02 5.02 0 0 0 11 15.9V19H7v2h10v-2h-4v-3.1a5.02 5.02 0 0 0 1.67-1.48c.69.37 1.49.58 2.33.58 2.76 0 5-2.24 5-5V5h-5V3M7 13a3 3 0 0 1-3-3V7h3v3c0 1.08.34 2.08.92 2.9-.29.06-.6.1-.92.1m13-3a3 3 0 0 1-3 3c-.32 0-.63-.04-.92-.1.58-.82.92-1.82.92-2.9V7h3v3Z"/></svg>' };
    Blocks.add('achievement-card', {
        label: 'lang::new_achievement',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<div class="achievements-showcase__item"><div class="achievements-showcase__badge">★</div><small>Nova conquista</small><strong>Título da conquista</strong><p>Descreva o objetivo alcançado pelo estudante.</p></div>`
    }, { at: 0 });
}
