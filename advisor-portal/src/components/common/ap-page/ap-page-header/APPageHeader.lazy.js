import React, { lazy, Suspense } from 'react';

const LazyAPPageHeader = lazy(() => import('./APPageHeader'));

const APPageHeader = props => (
  <Suspense fallback={null}>
    <LazyAPPageHeader {...props} />
  </Suspense>
);

export default APPageHeader;
