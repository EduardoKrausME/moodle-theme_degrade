editor => {
    const { Blocks } = editor;
    const category = { id: 'resources-grid', label: 'lang::resources_grid', icon: '<svg viewBox="0 0 24 24"><path d="M4 5h16v2H4V5m0 4h10v2H4V9m0 4h16v2H4v-2m0 4h10v2H4v-2Z"/></svg>' };
    Blocks.add('resources-grid-card', {
        label: 'lang::new_resource',
        media: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"/></svg>',
        category,
        content: `<article class="resources-grid__card"><div class="resources-grid__image"><img alt="lang::new_resource" src="https://picsum.photos/seed/new-resource/700/480"/><span>lang::resources_grid_artigo_059a22</span></div><div class="resources-grid__body"><small>lang::resources_grid_categoria_5_min_85f331</small><h3>lang::resources_grid_titulo_novo_conteudo_2bc28e</h3><a href="#">lang::resources_grid_ler_conteudo_33ff1c <b>→</b></a></div></article>`
    }, { at: 0 });
}
