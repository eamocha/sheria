import React, { lazy, Suspense } from 'react';

const LazyEditFileForm = lazy(() => import('./EditFileForm'));

const EditFileForm = props => (
  <Suspense fallback={null}>
    <LazyEditFileForm {...props} />
  </Suspense>
);

export default EditFileForm;
