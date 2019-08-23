import SwaggerUI from 'swagger-ui';

SwaggerUI({
    dom_id: '#docs',
    url: '/docs/openapi.json',
    defaultModelsExpandDepth: -1,
    docExpansion: 'none'
});
