import React, { lazy, Suspense } from 'react';

const LazyAPMainMenuItem = lazy(() => import('./APMainMenuItem'));

const APMainMenuItem = props => (
  <Suspense fallback={null}>
    <LazyAPMainMenuItem {...props} />
  </Suspense>
);

export default APMainMenuItem;
