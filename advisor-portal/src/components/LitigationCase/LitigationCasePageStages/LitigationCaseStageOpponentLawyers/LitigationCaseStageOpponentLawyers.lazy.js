import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageOpponentLawyers = lazy(() => import('./LitigationCaseStageOpponentLawyers'));

const LitigationCaseStageOpponentLawyers = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageOpponentLawyers {...props} />
  </Suspense>
);

export default LitigationCaseStageOpponentLawyers;
