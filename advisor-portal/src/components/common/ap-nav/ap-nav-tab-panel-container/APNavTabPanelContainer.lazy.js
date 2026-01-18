import React, { lazy, Suspense } from 'react';

const LazyAPNavTabPanelContainer= lazy(() => import('./APNavTabPanelContainer'));

const APNavTabPanelContainer= props => (
  <Suspense fallback={null}>
    <LazyAPNavTabPanelContainer {...props} />
  </Suspense>
);

export default APNavTabPanelContainer;
