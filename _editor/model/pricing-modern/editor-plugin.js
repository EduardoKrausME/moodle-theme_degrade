editor => {
    const { Blocks } = editor;
    const category = {
        id: 'prices',
        label: 'lang::prices',
        icon: '<svg viewBox="0 0 24 24"><path d="M7 11H1v2h6v-2m2.2-3.2L7 5.6 5.5 7.1l2.2 2 1.4-1.3M13 1h-2v6h2V1m5.4 6L17 5.7l-2.2 2.2 1.4 1.4L18.4 7M17 11v2h6v-2h-6m-5-2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m2.8 7.2 2.1 2.2 1.5-1.4-2.2-2.2-1.4 1.4m-9.2.8 1.5 1.4 2-2.2-1.3-1.4L5.6 17m5.4 6h2v-6h-2v6Z"/></svg>'
    };

    // Add new blocks
    Blocks.add('block-id-1', {
        label: 'lang::row_col_before_new',
        media: '<svg viewBox="0 0 24 24"><path d="M19,5H22V7H19V10H17V7H14V5H17V2H19V5M17,19V13H19V21H3V5H11V7H5V19H17Z" /></svg>',
        category: category,
        content: `
<div class="pricing-card">
<div class="card-header">
<h3>lang::pricing_modern_new_plan_06a18c</h3>
<p>lang::pricing_modern_companies_training_teams_3e58d1</p>
</div>
<div class="card-price">
<h2>$000 <span>lang::pricing_modern_month_ab0cc3</span></h2>
</div>
<ul class="card-features">
<li>lang::pricing_modern_team_management_8f1239</li>
<li>lang::pricing_modern_advanced_analytics_a8e165</li>
<li>lang::pricing_modern_dedicated_consultant_7d87fc</li>
<li>lang::pricing_modern_custom_integrations_cd978d</li>
</ul>
<a class="btn btn-primary w-100" href="#">lang::pricing_modern_contact_sales_7a0a9b</a>
</div>`,
    }, {
        at: 0 // Let's place this block at the beginning of the list
    });
}