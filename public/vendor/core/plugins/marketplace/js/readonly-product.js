document.addEventListener('DOMContentLoaded', function () {
    var inputReadOnlyIds = ['name', 'slug', 
        'specification_table_id',  'description', 'content',
        'images', 'video_media', 
        'sku', 'price', 'sale_price', 'cost_per_item', 'barcode',
        'weight', 'length', 'wide', 'height'
    ];  

    const overlayClasses = ['product-specification-table', 'meta-box-sortables', 'meta-boxes',
        'card-actions', 'add-new-product-attribute-wrap', 
        'product-option-form-wrap', 'wrap-relation-product', 'box-search-advance product'
    ];

    overlayClasses.forEach(className => {
        const elements = document.getElementsByClassName(className);
        Array.from(elements).forEach(wrapper => {
            if (!wrapper.querySelector('.specification-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'specification-overlay';
                wrapper.style.position = 'relative'; 
                wrapper.appendChild(overlay);
            }
        });
    });

    inputReadOnlyIds.forEach(id => {
        const label = document.querySelector(`label[for="${id}"]`);
        if (label) {
            const wrapper = label.closest('.mb-3, .form-group, .form-item, .form-field');
            
            if (wrapper && !wrapper.querySelector('.specification-overlay')) {
                const overlay = document.createElement('div');
                overlay.className = 'specification-overlay';
                
                if (getComputedStyle(wrapper).position === 'static') {
                    wrapper.style.position = 'relative';
                }

                wrapper.appendChild(overlay);
            }
        }
    });

    const container = document.querySelector('.gap-3.col-md-9');
    const target = document.getElementById('main-manage-product-type');

    if (container && target) {
        container.insertBefore(target, container.firstChild);
    }

});

