import React, { lazy, Suspense } from 'react';

const LazyEditForm = lazy(() => import('./EditForm'));

const EditForm = props => (
  <Suspense fallback={null}>
    <LazyEditForm {...props} />
  </Suspense>
);

export default EditForm;
