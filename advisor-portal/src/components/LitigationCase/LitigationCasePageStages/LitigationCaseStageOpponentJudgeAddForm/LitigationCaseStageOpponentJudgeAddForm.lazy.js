import React, { lazy, Suspense } from 'react';

const LazyLitigationCaseStageOpponentJudgeAddForm = lazy(() => import('./LitigationCaseStageOpponentJudgeAddForm'));

const LitigationCaseStageOpponentJudgeAddForm = props => (
  <Suspense fallback={null}>
    <LazyLitigationCaseStageOpponentJudgeAddForm {...props} />
  </Suspense>
);

export default LitigationCaseStageOpponentJudgeAddForm;
