import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePage = lazy(() => import('./LitigationCasePage'));

const LitigationCasePage = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePage {...props} />
  </Suspense>
);

export default LitigationCasePage;
