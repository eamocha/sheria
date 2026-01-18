import React, { lazy, Suspense } from 'react';

const LazyChooseCaseLitigationDetail = lazy(() => import('./ChooseCaseLitigationDetail'));

const ChooseCaseLitigationDetail = props => (
  <Suspense fallback={null}>
    <LazyChooseCaseLitigationDetail {...props} />
  </Suspense>
);

export default ChooseCaseLitigationDetail;
