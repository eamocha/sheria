import React, { lazy, Suspense } from 'react';

const LazyExternalReferencesEditForm = lazy(() => import('./ExternalReferencesEditForm'));

const ExternalReferencesEditForm = props => (
  <Suspense fallback={null}>
    <LazyExternalReferencesEditForm {...props} />
  </Suspense>
);

export default ExternalReferencesEditForm;
