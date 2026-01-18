import React, { lazy, Suspense } from 'react';

const LazyDialogHeader = lazy(() => import('./DialogHeader'));

const DialogHeader = props => (
  <Suspense fallback={null}>
    <LazyDialogHeader {...props} />
  </Suspense>
);

export default DialogHeader;
