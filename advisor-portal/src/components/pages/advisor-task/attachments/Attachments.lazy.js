import React, { lazy, Suspense } from 'react';

const LazyAttachments = lazy(() => import('./Attachments'));

const Attachments = props => (
  <Suspense fallback={null}>
    <LazyAttachments {...props} />
  </Suspense>
);

export default Attachments;
