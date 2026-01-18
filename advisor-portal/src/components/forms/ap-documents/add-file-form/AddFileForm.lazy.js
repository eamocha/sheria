import React, { lazy, Suspense } from 'react';

const LazyAddFileForm = lazy(() => import('./AddFileForm'));

const AddFileForm = props => (
  <Suspense fallback={null}>
    <LazyAddFileForm {...props} />
  </Suspense>
);

export default AddFileForm;
