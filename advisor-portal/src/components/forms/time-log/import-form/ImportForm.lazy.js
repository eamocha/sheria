import React, { lazy, Suspense } from 'react';

const LazyImportForm = lazy(() => import('./ImportForm'));

const ImportForm = props => (
  <Suspense fallback={null}>
    <LazyImportForm {...props} />
  </Suspense>
);

export default ImportForm;
