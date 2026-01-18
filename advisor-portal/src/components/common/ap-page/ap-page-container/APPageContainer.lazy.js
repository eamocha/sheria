import React, { lazy, Suspense } from 'react';

const LazyAPPageContainer = lazy(() => import('./APPageContainer'));

const APPageContainer = props => (
  <Suspense fallback={null}>
    <LazyAPPageContainer {...props} />
  </Suspense>
);

export default APPageContainer;
