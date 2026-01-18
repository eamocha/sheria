import React, { lazy, Suspense } from 'react';

const LazyDialogFooter = lazy(() => import('./DialogFooter'));

const DialogFooter = props => (
  <Suspense fallback={null}>
    <LazyDialogFooter {...props} />
  </Suspense>
);

export default DialogFooter;
