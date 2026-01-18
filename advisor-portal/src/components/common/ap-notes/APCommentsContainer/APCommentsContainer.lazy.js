import React, { lazy, Suspense } from 'react';

const LazyAPCommentsContainer = lazy(() => import('./APCommentsContainer'));

const APCommentsContainer = props => (
  <Suspense fallback={null}>
    <LazyAPCommentsContainer {...props} />
  </Suspense>
);

export default APCommentsContainer;
