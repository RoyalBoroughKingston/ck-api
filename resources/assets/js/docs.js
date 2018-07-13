window.$ = window.jQuery = require('jquery');
import SwaggerUI from 'swagger-ui';

const app = {
    docs: {
        coreApi: {
            id: '#core-api',
            url: '/docs/core/openapi.yaml',
            initialised: false
        },

        openReferralApi: {
            id: '#open-referral-api',
            url: '/docs/open-referral/openapi.yaml',
            initialised: false
        }
    },

    init() {
        this.swagger(this.docs.coreApi);
        this.bindEvents();
    },

    swagger(doc) {
        const swagger = SwaggerUI({
            dom_id: doc.id,
            url: doc.url,
            defaultModelsExpandDepth: -1,
            docExpansion: 'none'
        });

        doc.initialised = true;

        return swagger;
    },

    bindEvents() {
        const self = this;
        $('.sidebar-link').on('click', function (event) {
            event.preventDefault();
            const id = $(this).attr('href');
            self.onSidebarLinkClick(id);
        });
    },

    onSidebarLinkClick(id) {
        $('.swagger-ui__container').addClass('hidden');
        $(id).removeClass('hidden');

        const doc = this.getDocFromId(id);
        if (!doc.initialised) {
            this.swagger(doc);
        }
    },

    getDocFromId(id) {
        let doc = null;

        switch (id) {
            case this.docs.coreApi.id:
                doc = this.docs.coreApi;
                break
            case this.docs.openReferralApi.id:
                doc = this.docs.openReferralApi;
                break
            default:
                console.error(`The ID [${id}] does not correspond to any doc`);

        }

        return doc;
    }
};
app.init();

