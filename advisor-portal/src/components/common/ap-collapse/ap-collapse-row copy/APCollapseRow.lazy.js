import React, { lazy, Suspense } from 'react';

const LazyAPCollapseRow = lazy(() => import('./APCollapseRow'));

const APCollapseRow = props => (
  <Suspense fallback={null}>
    <LazyAPCollapseRow {...props} />
  </Suspense>
);

export default APCollapseRow;
