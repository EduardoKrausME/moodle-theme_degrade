editor => {
    const { Blocks } = editor;
    const category = { id: 'faq-support-modern', label: 'lang::faq_support_modern', icon: '<svg viewBox="0 0 24 24"><path d="M11 18h2v2h-2v-2m1-16a10 10 0 1 0 10 10A10 10 0 0 0 12 2m0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8m0-14a4 4 0 0 0-4 4h2a2 2 0 0 1 4 0c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5a4 4 0 0 0-4-4Z"/></svg>' };
    Blocks.add('faq-support-question', {
        label: 'lang::new_faq_question',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<details><summary>lang::faq_support_modern_digite_nova_pergunta_b8b344<span>+</span></summary><div>lang::faq_support_modern_adicione_aqui_resposta_completa_esta_duvida_5d0a57</div></details>`
    }, { at: 0 });
}
