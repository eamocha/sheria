import React, { lazy, Suspense } from 'react';

const LazyAddFolderForm = lazy(() => import('./AddFolderForm'));

const AddFolderForm = props => (
  <Suspense fallback={null}>
    <LazyAddFolderForm {...props} />
  </Suspense>
);

export default AddFolderForm;
