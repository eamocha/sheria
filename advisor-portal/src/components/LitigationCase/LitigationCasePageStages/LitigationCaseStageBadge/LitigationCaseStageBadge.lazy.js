import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageBadge = lazy(() => import('./LitigationCaseStageBadge'));

const LitigationCaseStageBadge = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageBadge {...props} />
  </Suspense>
);

export default LitigationCaseStageBadge;
