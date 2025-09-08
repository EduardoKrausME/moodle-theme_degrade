editor => {
    const {Blocks} = editor;
    const category = {
        id: 'events',
        label: 'lang::events',
        icon: '<svg viewBox="0 0 24 24"><path d="M7 11H1v2h6v-2m2.2-3.2L7 5.6 5.5 7.1l2.2 2 1.4-1.3M13 1h-2v6h2V1m5.4 6L17 5.7l-2.2 2.2 1.4 1.4L18.4 7M17 11v2h6v-2h-6m-5-2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3m2.8 7.2 2.1 2.2 1.5-1.4-2.2-2.2-1.4 1.4m-9.2.8 1.5 1.4 2-2.2-1.3-1.4L5.6 17m5.4 6h2v-6h-2v6Z"/></svg>'
    };

    // Add new blocks
    Blocks.add('block-id-1', {
        label: 'lang::row_col_before_new',
        media: '<svg viewBox="0 0 24 24"><path d="M19,5H22V7H19V10H17V7H14V5H17V2H19V5M17,19V13H19V21H3V5H11V7H5V19H17Z" /></svg>',
        category: category,
        content: `
            <div class="col-lg-4 col-md-6 col-sm-12 event-block">
                <div class="event-card">
                    <div class="event-date">
                        <span class="day">12</span>
                        <span class="month">SEP</span>
                    </div>
                    <h3 class="event-title">Event name</h3>
                    <p class="event-desc">A hands-on workshop exploring new approaches in digital education.</p>
                    <a href="#" class="event-link">lang::learn_more →</a>
                </div>
            </div>`,
    }, {
        at: 0 // Let's place this block at the beginning of the list
    });
}