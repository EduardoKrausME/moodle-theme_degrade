editor => {
    const { Blocks } = editor;
    const category = { id: 'help-center-modern', label: 'lang::help_center_modern', icon: '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2m1 17h-2v-2h2v2m2.07-7.25-.9.92A3.49 3.49 0 0 0 13 15h-2v-.5c0-.8.32-1.57.88-2.13l1.24-1.26A2 2 0 1 0 9.76 9H7.73a4 4 0 1 1 7.34 2.75Z"/></svg>' };
    Blocks.add('help-center-topic', {
        label: 'lang::new_help_topic',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<a class="help-center-modern__card" href="#"><span class="help-center-modern__icon">?</span><div><strong>lang::help_center_modern_novo_assunto_be40ca</strong><small>lang::help_center_modern_breve_descricao_conteudo_ajuda_534f81</small></div><b>lang::help_center_modern_5_artigos_d77457</b></a>`
    }, { at: 0 });
}
