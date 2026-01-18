import React, { lazy, Suspense } from 'react';

const LazyExternalReferences = lazy(() => import('./ExternalReferences'));

const ExternalReferences = props => (
  <Suspense fallback={null}>
    <LazyExternalReferences {...props} />
  </Suspense>
);

export default ExternalReferences;
