import React, { lazy, Suspense } from 'react';

const LazyEditFolderForm = lazy(() => import('./EditFolderForm'));

const EditFolderForm = props => (
  <Suspense fallback={null}>
    <LazyEditFolderForm {...props} />
  </Suspense>
);

export default EditFolderForm;
