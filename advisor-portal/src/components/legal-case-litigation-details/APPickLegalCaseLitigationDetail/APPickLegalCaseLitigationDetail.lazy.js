import React, { lazy, Suspense } from 'react';

const LazyAPPickLegalCaseLitigationDetail = lazy(() => import('./APPickLegalCaseLitigationDetail'));

const APPickLegalCaseLitigationDetail = props => (
  <Suspense fallback={null}>
    <LazyAPPickLegalCaseLitigationDetail {...props} />
  </Suspense>
);

export default APPickLegalCaseLitigationDetail;
