editor => {
    const { Blocks } = editor;
    const category = { id: 'learning-journey', label: 'lang::learning_journey', icon: '<svg viewBox="0 0 24 24"><path d="M6 2a4 4 0 0 1 4 4c0 1.86-1.28 3.42-3 3.86V15h4v-2h2v2h4v-5.14A4 4 0 1 1 19 10v7H7v1a2 2 0 0 0 2 2h11v2H9a4 4 0 0 1-4-4V9.86A4 4 0 0 1 6 2m0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4m13 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>' };
    Blocks.add('learning-journey-step', {
        label: 'lang::new_journey_step',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<div class="learning-journey__step"><div class="learning-journey__number">05</div><div class="learning-journey__step-content"><span>lang::learning_journey_nova_etapa_047076</span><h3>lang::learning_journey_titulo_etapa_43b7b9</h3><p>lang::learning_journey_descreva_acontece_neste_momento_jornada_aprendizagem_7d4371</p></div></div>`
    }, { at: 0 });
}
