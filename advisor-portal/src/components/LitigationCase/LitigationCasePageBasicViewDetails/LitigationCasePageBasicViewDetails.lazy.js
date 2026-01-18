import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewDetails = lazy(() => import('./LitigationCasePageBasicViewDetails'));

const LitigationCasePageBasicViewDetails = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewDetails {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewDetails;
