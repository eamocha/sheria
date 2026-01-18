import React, { lazy, Suspense } from 'react';

const LazyAPCollapseRowItem = lazy(() => import('./APCollapseRowItem'));

const APCollapseRowItem = props => (
  <Suspense fallback={null}>
    <LazyAPCollapseRowItem {...props} />
  </Suspense>
);

export default APCollapseRowItem;
