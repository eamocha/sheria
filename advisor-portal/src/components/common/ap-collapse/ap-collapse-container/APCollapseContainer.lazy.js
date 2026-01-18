import React, { lazy, Suspense } from 'react';

const LazyAPCollapseContainer = lazy(() => import('./APCollapseContainer'));

const APCollapseContainer = props => (
  <Suspense fallback={null}>
    <LazyAPCollapseContainer {...props} />
  </Suspense>
);

export default APCollapseContainer;
