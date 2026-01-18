import React, { lazy, Suspense } from 'react';

const LazyAddForm = lazy(() => import('./AddForm'));

const AddForm = props => (
  <Suspense fallback={null}>
    <LazyAddForm {...props} />
  </Suspense>
);

export default AddForm;
