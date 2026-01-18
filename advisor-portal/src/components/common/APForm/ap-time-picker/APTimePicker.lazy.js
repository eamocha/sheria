import React, { lazy, Suspense } from 'react';

const LazyAPTimePicker = lazy(() => import('./APTimePicker'));

const APTimePicker = props => (
  <Suspense fallback={null}>
    <LazyAPTimePicker {...props} />
  </Suspense>
);

export default APTimePicker;
