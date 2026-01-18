import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewContributors = lazy(() => import('./LitigationCasePageBasicViewContributors'));

const LitigationCasePageBasicViewContributors = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewContributors {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewContributors;
