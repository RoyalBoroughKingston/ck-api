import SwaggerUI from 'swagger-ui';

SwaggerUI({
    dom_id: '#docs',
    url: '/docs/core/openapi.yaml',
    defaultModelsExpandDepth: -1,
    docExpansion: 'none'
});
