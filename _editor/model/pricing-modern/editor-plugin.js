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
                    <h3>New plan</h3>
                    <p>For companies and training teams</p>
                </div>
                <div class="card-price">
                    <h2>$000 <span>/month</span></h2>
                </div>
                <ul class="card-features">
                    <li>✔ Team management</li>
                    <li>✔ Advanced analytics</li>
                    <li>✔ Dedicated consultant</li>
                    <li>✔ Custom integrations</li>
                </ul>
                <a href="#" class="btn btn-primary w-100">Contact Sales</a>
            </div>`,
    }, {
        at: 0 // Let's place this block at the beginning of the list
    });
}