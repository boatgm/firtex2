//
// Copyright(C) 2005--2011 FirteX Development Team. 
// All rights reserved.
// This file is part of FirteX (www.firtex.org)
//
// Use of the FirteX is subject to the terms of the software license set forth in 
// the LICENSE file included with this software, and also available at
// http://www.firtex.org/license.html
//
// Author	: �����(GuoRuijie)
// Email	: ruijieguo@software.ict.ac.cn
// Created	: 2006/1/4
//
#ifndef __FX_LENGTHNORMITERATOR_H
#define __FX_LENGTHNORMITERATOR_H

#include "firtex/common/StdHeader.h"
#include "firtex/common/SharedPtr.h"
#include "firtex/index/TypedForwardIndexIterator.h"
#include "firtex/index/forwardindex/PrimitiveTypeForwardIndexReaderMMapImpl.h"

FX_NS_DEF(index);

typedef TypedForwardIndexIterator<int32_t> LengthNormIterator;
DEFINE_TYPED_PTR(LengthNormIterator);

FX_NS_END


#endif
