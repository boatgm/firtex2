#include "firtex/search/TermQuery.h"
#include "firtex/search/TermQueryExecutor.h"
#include <sstream>

using namespace std;
FX_NS_USE(utility);
FX_NS_USE(index);

FX_NS_DEF(search);

SETUP_STREAM_LOGGER(search, TermQuery);

TermQuery::TermQuery(Term* pTerm) 
    : m_pTerm(pTerm)
{
}

TermQuery::TermQuery(const TermPtr& pTerm) 
    : m_pTerm(pTerm)
{
}

TermQuery::TermQuery(const TermQuery& src) 
    : m_pTerm(src.m_pTerm)
{
}

TermQuery::~TermQuery() 
{
}

string TermQuery::getIdentifier() const
{
    return "TermQuery";
}

size_t TermQuery::size() const
{
    return 1;
}

QueryExecutorPtr TermQuery::createExecutor(IndexReaderPtr& pIndexReader,
        FeatureProviderPtr& pProvider, PoolPtr& pPool) const
{
    TermReaderPtr pTermReader = pIndexReader->termReader();

    TermPostingIteratorPtr pPostIter = pTermReader->seek(m_pTerm.get());
    if (!pPostIter)
    {
        return QueryExecutorPtr();
    }

    QueryExecutorPtr pExe(new TermQueryExecutor(this, pPostIter,
                    pProvider, pPool));
    return pExe;
}

Query* TermQuery::clone() const
{
    return new TermQuery(*this);
}

string TermQuery::toString() const
{
    stringstream ss;
    ss << "TermQuery: [";
    if (m_pTerm)
    {
        ss << m_pTerm->toString();
    }
    ss << "]";
    return ss.str();
}

void TermQuery::terms(TermVector& terms) const
{
    terms.push_back(m_pTerm);
}

FX_NS_END

