import React, { lazy, Suspense } from 'react';

const LazyAPNotificationBar = lazy(() => import('./APNotificationBar'));

const APNotificationBar = props => (
  <Suspense fallback={null}>
    <LazyAPNotificationBar {...props} />
  </Suspense>
);

export default APNotificationBar;
