import React, { lazy, Suspense } from 'react';

const LazyAPDatePicker = lazy(() => import('./APDatePicker'));

const APDatePicker = props => (
  <Suspense fallback={null}>
    <LazyAPDatePicker {...props} />
  </Suspense>
);

export default APDatePicker;
