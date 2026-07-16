editor => {
    const { Blocks } = editor;
    const category = { id: 'learning-paths-modern', label: 'lang::learning_paths_modern', icon: '<svg viewBox="0 0 24 24"><path d="M5 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6m14 12a3 3 0 1 0 0 6 3 3 0 0 0 0-6M5 5a1 1 0 1 1 0 2 1 1 0 0 1 0-2m14 12a1 1 0 1 1 0 2 1 1 0 0 1 0-2M8 5h7a4 4 0 0 1 4 4v3h-2V9a2 2 0 0 0-2-2H8V5m-3 6h2v4a2 2 0 0 0 2 2h7v2H9a4 4 0 0 1-4-4v-4Z"/></svg>' };
    Blocks.add('learning-path-card', {
        label: 'lang::new_learning_path',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<article class="learning-paths-modern__card"><div class="learning-paths-modern__top"><span>04</span><small>Nível</small></div><h3>Nova trilha de aprendizagem</h3><p>Descreva os resultados e conhecimentos trabalhados nesta trilha.</p><div class="learning-paths-modern__courses"><span>4 cursos</span><span>20 horas</span><span>Certificado</span></div><div class="learning-paths-modern__bar"><span style="width:45%"></span></div><a href="#">Explorar trilha <b>→</b></a></article>`
    }, { at: 0 });
}
