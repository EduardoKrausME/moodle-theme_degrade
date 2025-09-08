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
            <div class="col-lg-4 col-md-6 col-sm-12 pricing-block">
                <div class="pricing-table">
                    <div class="table-header">
                        <div data-type-icon class="gjs-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path fill="currentColor"
                                      d="M160 64c0-35.3 28.7-64 64-64L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64l-239.2 0c-11.8-25.5-29.9-47.5-52.4-64l99.6 0 0-32c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 32 64 0 0-288L224 64l0 49.1C205.2 102.2 183.3 96 160 96l0-32zm0 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zM133.3 352l53.3 0C260.3 352 320 411.7 320 485.3c0 14.7-11.9 26.7-26.7 26.7L26.7 512C11.9 512 0 500.1 0 485.3C0 411.7 59.7 352 133.3 352z"/>
                            </svg>
                        </div>
                        <h3>New Plan</h3>
                    </div>
                    <div class="table-content">
                        <h3>$000 <span>/ month</span></h3>
                        <p>Ideal for taking your first steps in digital learning.</p>
                        <a href="#" class="btn btn-primary w-100">Start Now</a>
                        <ul class="list-style-one clearfix">
                            <li>Postgraduate teachers</li>
                            <li>Digital certificate included</li>
                            <li>Access to collaborative forums</li>
                            <li>Intuitive environment</li>
                        </ul>
                    </div>
                </div>
            </div>`,
    }, {
        at: 0 // Let's place this block at the beginning of the list
    });
}