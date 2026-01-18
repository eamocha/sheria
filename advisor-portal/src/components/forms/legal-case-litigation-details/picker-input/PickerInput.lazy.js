import React, { lazy, Suspense } from 'react';

const LazyPickerInput = lazy(() => import('./PickerInput'));

const PickerInput = props => (
  <Suspense fallback={null}>
    <LazyPickerInput {...props} />
  </Suspense>
);

export default PickerInput;
