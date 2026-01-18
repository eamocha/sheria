import React, { lazy, Suspense } from 'react';

const LazyExternalReferencesForm = lazy(() => import('./ExternalReferencesForm'));

const ExternalReferencesForm = props => (
  <Suspense fallback={null}>
    <LazyExternalReferencesForm {...props} />
  </Suspense>
);

export default ExternalReferencesForm;
