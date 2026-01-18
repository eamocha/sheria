import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageOpponentJudges = lazy(() => import('./LitigationCaseStageOpponentJudges'));

const LitigationCaseStageOpponentJudges = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageOpponentJudges {...props} />
  </Suspense>
);

export default LitigationCaseStageOpponentJudges;
