import React, { lazy, Suspense } from 'react';

const LazyLitigationCasesPage = lazy(() => import('./LitigationCasesPage'));

const LitigationCasesPage = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasesPage {...props} />
  </Suspense>
);

export default LitigationCasesPage;
