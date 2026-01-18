import React, { lazy, Suspense } from 'react';

const LazyAPDateTimePicker = lazy(() => import('./APDateTimePicker'));

const APDateTimePicker = props => (
  <Suspense fallback={null}>
    <LazyAPDateTimePicker {...props} />
  </Suspense>
);

export default APDateTimePicker;
